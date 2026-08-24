$('#modalDesactivar').on('show.bs.modal', function (event) {
    const boton = $(event.relatedTarget);
    const id = boton.data('id');
    const nombre = boton.data('nombre');

    const modal = $(this);

    modal
        .find('#nombreServicioDesactivar')
        .text(nombre);

    modal
        .find('#formDesactivar')
        .attr(
            'action',
            "{{ url('servicios-experiencias') }}/" + id
        );
});

$('#modalActivar').on('show.bs.modal', function (event) {
    const boton = $(event.relatedTarget);
    const id = boton.data('id');
    const nombre = boton.data('nombre');

    const modal = $(this);

    modal
        .find('#nombreServicioActivar')
        .text(nombre);

    modal
        .find('#formActivar')
        .attr(
            'action',
            "{{ url('servicios-experiencias') }}/" + id +
        '/activar'
        );
});