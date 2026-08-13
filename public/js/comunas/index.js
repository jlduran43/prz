$(document).ready(function () {

    $('#modalCambiarEstadoComuna').on('show.bs.modal', function (event) {

        const boton = $(event.relatedTarget);

        const id = boton.data('id');
        const nombre = boton.data('nombre');
        const activo = Number(boton.data('activo')) === 1;

        const modal = $(this);

        const form = modal.find('#formCambiarEstadoComuna');
        const titulo = modal.find('#modalCambiarEstadoComunaLabel');
        const mensaje = modal.find('#mensajeCambiarEstadoComuna');
        const botonConfirmar = modal.find('#botonConfirmarEstadoComuna');

        form.attr('action', `/comunas/${id}/cambiar-estado`);

        botonConfirmar.removeClass(
            'btn-danger btn-success btn-primary btn-warning'
        );

        if (activo) {

            titulo.text('Desactivar comuna');

            mensaje.html(
                `¿Deseas desactivar la comuna <strong>${nombre}</strong>?`
            );

            botonConfirmar
                .addClass('btn-danger')
                .text('Sí, desactivar');

        } else {

            titulo.text('Activar comuna');

            mensaje.html(
                `¿Deseas activar la comuna <strong>${nombre}</strong>?`
            );

            botonConfirmar
                .addClass('btn-success')
                .text('Sí, activar');
        }

    });

});
