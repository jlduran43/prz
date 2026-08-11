$('#modalCambiarEstadoRegion').on(
    'show.bs.modal',
    function (event) {

        const boton =
            $(event.relatedTarget);

        const id =
            boton.data('id');

        const nombre =
            boton.data('nombre');

        const activo =
            Number(
                boton.data('activo')
            );

        const modal =
            $(this);

        const formulario =
            modal.find(
                '#formCambiarEstadoRegion'
            );

        const mensaje =
            modal.find(
                '#mensajeCambiarEstadoRegion'
            );

        const botonConfirmar =
            modal.find(
                '#botonConfirmarEstadoRegion'
            );

        const urlBase =
            formulario.data('url-base');

        formulario.attr(
            'action',
            urlBase
                + '/'
                + id
                + '/cambiar-estado'
        );

        if (activo === 1) {

            modal.find(
                '.modal-title'
            ).text(
                'Desactivar región'
            );

            mensaje.html(
                '¿Deseas desactivar la región '
                + '<strong>'
                + nombre
                + '</strong>?'
            );

            botonConfirmar
                .removeClass(
                    'btn-success'
                )
                .addClass(
                    'btn-danger'
                )
                .text(
                    'Sí, desactivar'
                );

        } else {

            modal.find(
                '.modal-title'
            ).text(
                'Activar región'
            );

            mensaje.html(
                '¿Deseas activar la región '
                + '<strong>'
                + nombre
                + '</strong>?'
            );

            botonConfirmar
                .removeClass(
                    'btn-danger'
                )
                .addClass(
                    'btn-success'
                )
                .text(
                    'Sí, activar'
                );
        }
    }
);
