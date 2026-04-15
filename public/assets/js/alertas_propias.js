const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
});

const ToastLG = (options) => {
        Swal.fire({
            showConfirmButton: (typeMsg == 'error') ? false : true,
            showDenyButton: timeout,
            showCancelButton: (typeMsg == 'success') ? false : true,
            confirmButtonText: 'Aceptar',
            denyButtonText: 'Reintentar',
            cancelButtonText: 'Cancelar',
            timerProgressBar: true,
            allowOutsideClick: false,
            ...options
        }).then((result) => {

            if (result.isConfirmed) {
                espera('Recargando...');
                location.reload(); // opción 1
            }

            else if (result.isDenied) {
                guardar_solicitud()
            }

            else if (result.isDismissed) {
                console.log('El usuario canceló'); // opción 3
            }

        });
    };
