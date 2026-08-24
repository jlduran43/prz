$(document).ready(function () {

    $('#modalCambiarEstadoCategoria').on(
        'show.bs.modal',
        function (event) {

            const boton = $(event.relatedTarget);

            const id = boton.data('id');
            const nombre = boton.data('nombre');
            const activo =
                Number(boton.data('activo')) === 1;

            const modal = $(this);

            const form = modal.find(
                '#formCambiarEstadoCategoria'
            );

            const titulo = modal.find(
                '#modalCambiarEstadoCategoriaLabel'
            );

            const mensaje = modal.find(
                '#mensajeCambiarEstadoCategoria'
            );

            const botonConfirmar = modal.find(
                '#botonConfirmarEstadoCategoria'
            );

            form.attr(
                'action',
                `/categorias-servicio/${id}/activar`
            );

            botonConfirmar.removeClass(
                'btn-danger btn-success btn-primary btn-warning btn-secondary'
            );

            if (activo) {

                titulo.text(
                    'Desactivar categoría de servicio'
                );

                mensaje.html(
                    `¿Deseas desactivar la categoría de servicio <strong>${nombre}</strong>?`
                );

                botonConfirmar
                    .addClass('btn-danger')
                    .text('Sí, desactivar');

            } else {

                titulo.text(
                    'Activar categoría de servicio'
                );

                mensaje.html(
                    `¿Deseas activar la categoría de servicio <strong>${nombre}</strong>?`
                );

                botonConfirmar
                    .addClass('btn-success')
                    .text('Sí, activar');
            }

        }
    );

});