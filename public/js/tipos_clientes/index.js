$('#modalCambiarEstado').on(
    'show.bs.modal',
    function (event) {
        const boton = $(event.relatedTarget);

        const id = boton.data('id');
        const nombre = boton.data('nombre');
        const activo = Number(
            boton.data('activo')
        );

        const modal = $(this);

        const form = modal.find(
            '#formCambiarEstado'
        );

        const mensaje = modal.find(
            '#mensajeCambiarEstado'
        );

        const botonConfirmar = modal.find(
            '#botonConfirmarEstado'
        );

        form.attr(
            'action',
            "{{ url('tipos - cliente') }}/"
            + id
        + '/cambiar-estado'
        );

        if (activo === 1) {
            modal.find('.modal-title').text(
                'Desactivar tipo de cliente'
            );

            mensaje.html(
                '¿Deseas desactivar el tipo de cliente '
                + '<strong>'
                + nombre
                + '</strong>?'
            );

            botonConfirmar
                .removeClass('btn-success')
                .addClass('btn-danger')
                .text('Sí, desactivar');
        } else {
            modal.find('.modal-title').text(
                'Activar tipo de cliente'
            );

            mensaje.html(
                '¿Deseas activar el tipo de cliente '
                + '<strong>'
                + nombre
                + '</strong>?'
            );

            botonConfirmar
                .removeClass('btn-danger')
                .addClass('btn-success')
                .text('Sí, activar');
        }
    }
);
