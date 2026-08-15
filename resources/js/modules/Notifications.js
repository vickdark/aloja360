export default class Notifications {
    static baseConfig = {
        confirmButtonColor: '#4e73df',
        cancelButtonColor: '#858796',
        customClass: {
            popup: 'rounded-4 shadow-lg',
            confirmButton: 'btn btn-primary rounded-pill px-4',
            cancelButton: 'btn btn-secondary rounded-pill px-4'
        },
        buttonsStyling: false
    };

    static success(message, title = '¡Éxito!') {
        return window.Swal.fire({
            ...this.baseConfig,
            icon: 'success',
            title: title,
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    }

    static error(message, title = '¡Error!') {
        return window.Swal.fire({
            ...this.baseConfig,
            icon: 'error',
            title: title,
            text: message
        });
    }

    static warning(message, title = 'Atención') {
        return window.Swal.fire({
            ...this.baseConfig,
            icon: 'warning',
            title: title,
            text: message
        });
    }

    static info(message, title = 'Información') {
        return window.Swal.fire({
            ...this.baseConfig,
            icon: 'info',
            title: title,
            text: message
        });
    }

    static async confirm(options = {}) {
        const config = {
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            ...this.baseConfig,
            ...options
        };

        const result = await window.Swal.fire(config);
        return result.isConfirmed;
    }

    static toast(message, icon = 'success') {
        const Toast = window.Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', window.Swal.stopTimer)
                toast.addEventListener('mouseleave', window.Swal.resumeTimer)
            }
        });

        return Toast.fire({
            icon: icon,
            title: message
        });
    }

    static loading(message = 'Procesando...') {
        return window.Swal.fire({
            ...this.baseConfig,
            title: message,
            allowOutsideClick: false,
            didOpen: () => {
                window.Swal.showLoading();
            }
        });
    }
}
