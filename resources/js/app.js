import './bootstrap';
import * as bootstrap from 'bootstrap';
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
import { initInventoryIndex } from './pages/inventory/index';
import { initGuestsIndex } from './pages/guests/index';
import { initReservationsIndex } from './pages/reservations/index';
import { initQuotesIndex } from './pages/quotes/index';
import { initPaymentsIndex } from './pages/payments/index';
import { initBlockedPeriodsIndex } from './pages/blocked_periods/index';
import { initRatePeriodsIndex } from './pages/rate_periods/index';
import { initRolesIndex } from './pages/roles/index';
import { initCleaningIndex } from './pages/cleaning/index';
import { initMaintenanceIndex } from './pages/maintenance/index';
import { initExpensesIndex } from './pages/expenses/index';
import { initBusinessesIndex } from './pages/businesses/index';

//Opciones de expotacion para Grid JS 
import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';
import * as XLSX from 'xlsx';


// Global Assignments
window.bootstrap = bootstrap;
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
window.initInventoryIndex = initInventoryIndex;
window.initGuestsIndex = initGuestsIndex;
window.initReservationsIndex = initReservationsIndex;
window.initQuotesIndex = initQuotesIndex;
window.initPaymentsIndex = initPaymentsIndex;
window.initBlockedPeriodsIndex = initBlockedPeriodsIndex;
window.initRatePeriodsIndex = initRatePeriodsIndex;
window.initRolesIndex = initRolesIndex;
window.initCleaningIndex = initCleaningIndex;
window.initMaintenanceIndex = initMaintenanceIndex;
window.initExpensesIndex = initExpensesIndex;
window.initBusinessesIndex = initBusinessesIndex;

// Initialize
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
});
