$('#modalCambiarEstadoCategoria').on(
            'show.bs.modal',
            function(event) {

                const boton = $(event.relatedTarget);

                const id = boton.data('id');
                const nombre = boton.data('nombre');
                const activo = boton.data('activo');

                const modal = $(this);

                const form = modal.find(
                    '#formCambiarEstadoCategoria'
                );

                const mensaje = modal.find(
                    '#mensajeCambiarEstadoCategoria'
                );

                const botonConfirmar = modal.find(
                    '#botonConfirmarEstadoCategoria'
                );


                if (activo == 1) {

                    mensaje.html(
                        '¿Está seguro de que desea desactivar la categoría <strong>' +
                        nombre +
                        '</strong>?'
                    );

                    form.attr(
                        'action',
                        '{{ url('categorias-servicio') }}/' + id
                    );

                    form.find('input[name="_method"]').remove();

                    form.append(
                        '<input type="hidden" name="_method" value="DELETE">'
                    );

                    botonConfirmar
                        .removeClass(
                            'btn-success'
                        )
                        .addClass(
                            'btn-secondary'
                        )
                        .html(
                            '<i class="fas fa-ban mr-1"></i> Desactivar'
                        );

                } else {

                    mensaje.html(
                        '¿Está seguro de que desea activar la categoría <strong>' +
                        nombre +
                        '</strong>?'
                    );

                    form.attr(
                        'action',
                        '{{ url('categorias-servicio') }}/' +
                        id +
                        '/activar'
                    );

                    form.find('input[name="_method"]').remove();

                    form.append(
                        '<input type="hidden" name="_method" value="PATCH">'
                    );

                    botonConfirmar
                        .removeClass(
                            'btn-secondary'
                        )
                        .addClass(
                            'btn-success'
                        )
                        .html(
                            '<i class="fas fa-check mr-1"></i> Activar'
                        );

                }

            }
        );