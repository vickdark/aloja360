export function initSidebar() {
    const sidebarToggles = document.querySelectorAll('[data-toggle="sidebar-mini"]');
    const body = document.body;

    sidebarToggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            if (window.innerWidth <= 991) {
                // En móviles, abrimos/cerramos el sidebar completo
                body.classList.toggle('sidebar-open');
            } else {
                // En desktop, usamos el modo mini
                body.classList.toggle('sidebar-mini');
            }
        });
    });
}
