import './bootstrap';
import 'bootstrap';
import '@fortawesome/fontawesome-free/js/all.js';
import Alpine from 'alpinejs';
import TomSelect from 'tom-select';
import { Grid, html } from 'gridjs';
import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';

// Import Modules
import DataGrid from './modules/DataGrid';
import { initSidebar } from './modules/sidebar';
import Notifications from './modules/Notifications';

// Import Pages
import { initUsersIndex } from './pages/usuarios/index';

//Opciones de expotacion para Grid JS 
import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';
import * as XLSX from 'xlsx';


// Global Assignments
window.Alpine = Alpine;
window.TomSelect = TomSelect;
window.Gridjs = { Grid, html };
window.Swal = Swal;
window.Chart = Chart;
window.jsPDF = jsPDF;
window.autoTable = autoTable;
window.XLSX = XLSX;
window.DataGrid = DataGrid;
window.Notify = Notifications;
window.initUsersIndex = initUsersIndex;

// Initialize
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
});
